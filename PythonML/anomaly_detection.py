# anomaly_detection_pipeline.py

import os
import logging
from datetime import datetime
from typing import Optional, List

import pandas as pd
from sqlalchemy import text
from sklearn.ensemble import IsolationForest
from sklearn.preprocessing import StandardScaler
from sklearn.decomposition import PCA
from datetime import datetime, UTC

from db import get_engine, test_db_connection

# ------------------ Configuration ------------------

class Config:
    """Centralized configuration for anomaly detection pipeline."""
    REQUIRED_TABLES = {'transactions', 'products'}
    CONTAMINATION_RATE = float(os.getenv('CONTAMINATION_RATE', 0.05))
    RANDOM_STATE = int(os.getenv('RANDOM_STATE', 42))
    BATCH_SIZE = int(os.getenv('BATCH_SIZE', 1000))
    USE_PCA = os.getenv('USE_PCA', 'False').lower() == 'true'
    PRODUCT_ANOMALY_THRESHOLD = float(os.getenv('PRODUCT_ANOMALY_THRESHOLD', 0.3))  # 30%

# Set up structured logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(name)s - %(message)s"
)
logger = logging.getLogger('AnomalyDetectionPipeline')

# Database engine
engine = get_engine()

# ------------------ Constants ------------------

class AnomalyStatus:
    Normal = 'normal'
    Anomalous = 'anomalous'

EXPECTED_COLUMNS = [
    'transaction_id', 'product_id', 'anomaly_score', 'status', 'created_at', 'updated_at'
]

# ------------------ Data Layer ------------------

def fetch_data() -> Optional[pd.DataFrame]:
    """Fetch transaction and product data from database."""
    query = """
        SELECT t.id AS transaction_id, t.product_id, t.quantity, t.type, p.price, t.created_at, t.created_by
        FROM transactions t
        JOIN products p ON t.product_id = p.id
    """
    try:
        data_df = pd.read_sql(query, engine)
        logger.info(f"Fetched {len(data_df)} transaction records.")
        return data_df
    except Exception as e:
        logger.exception("Failed to fetch data.")
        return None

# ------------------ Feature Engineering ------------------

def feature_engineering(df: pd.DataFrame) -> pd.DataFrame:
    """Prepare features for anomaly detection."""
    df = df.copy()

    df['user_id'] = df['created_by'].fillna(0)
    df['transaction_value'] = df['quantity'] * df['price']

    df['created_at'] = pd.to_datetime(df['created_at'])
    df['hour'] = df['created_at'].dt.hour
    df['day_of_week'] = df['created_at'].dt.dayofweek
    df['transaction_type'] = df['type'].astype('category').cat.codes

    return df

# ------------------ Anomaly Detection ------------------

def detect_anomalies(df: pd.DataFrame) -> pd.DataFrame:
    """Run anomaly detection on prepared dataset."""
    df = feature_engineering(df)

    feature_cols = ['quantity', 'transaction_value', 'hour', 'day_of_week', 'transaction_type']

    # Standard scaling
    scaler = StandardScaler()
    features_scaled = scaler.fit_transform(df[feature_cols])

    if Config.USE_PCA:
        pca = PCA(n_components=2, random_state=Config.RANDOM_STATE)
        features_scaled = pca.fit_transform(features_scaled)
        logger.info("Applied PCA for dimensionality reduction.")

    model = IsolationForest(
        contamination=Config.CONTAMINATION_RATE,
        random_state=Config.RANDOM_STATE
    )
    df['anomaly_score'] = model.fit_predict(features_scaled)

    df['status'] = df['anomaly_score'].apply(
        lambda x: AnomalyStatus.Anomalous if x == -1 else AnomalyStatus.Normal
    )

    logger.info(f"Anomaly detection completed on {len(df)} records.")
    return df[['transaction_id', 'product_id', 'anomaly_score', 'status']]

def aggregate_product_anomalies(transactions_df: pd.DataFrame) -> pd.DataFrame:
    """Aggregate anomaly status at product level."""
    product_anomaly_rate = (
        transactions_df.groupby('product_id')['status']
        .apply(lambda x: (x == AnomalyStatus.Anomalous).mean())
        .reset_index(name='anomaly_rate')
    )
    product_anomaly_rate['product_status'] = product_anomaly_rate['anomaly_rate'].apply(
        lambda x: AnomalyStatus.Anomalous if x >= Config.PRODUCT_ANOMALY_THRESHOLD else AnomalyStatus.Normal
    )
    return product_anomaly_rate

# ------------------ Persistence Layer ------------------

def validate_columns(df: pd.DataFrame, expected: List[str]) -> bool:
    """Ensure DataFrame matches required schema."""
    missing = set(expected) - set(df.columns)
    if missing:
        logger.error(f"DataFrame missing columns: {missing}")
        return False
    return True

def store_anomaly_results(df: pd.DataFrame, table_name: str = 'anomaly_detection_results') -> None:
    """Insert anomaly results into database."""
    timestamp = datetime.now(UTC).strftime('%Y-%m-%d %H:%M:%S')

    df = df.copy()
    df['created_at'] = timestamp
    df['updated_at'] = timestamp

    if not validate_columns(df, EXPECTED_COLUMNS):
        logger.error("Aborting insert due to schema mismatch.")
        return

    try:
        with engine.begin() as connection:
            records = df.to_dict(orient='records')
            stmt = text(f"""
                INSERT INTO {table_name}
                (transaction_id, product_id, anomaly_score, status, created_at, updated_at)
                VALUES (:transaction_id, :product_id, :anomaly_score, :status, :created_at, :updated_at)
            """)
            for batch_start in range(0, len(records), Config.BATCH_SIZE):
                batch = records[batch_start:batch_start + Config.BATCH_SIZE]
                connection.execute(stmt, batch)
        logger.info(f"Inserted {len(df)} records into {table_name}.")
    except Exception as e:
        logger.exception("Failed to store anomaly detection results.")

# ------------------ Monitoring ------------------

def log_summary(anomalies_df: pd.DataFrame) -> None:
    """Print summary metrics."""
    total = len(anomalies_df)
    anomalous = (anomalies_df['status'] == AnomalyStatus.Anomalous).sum()
    anomaly_rate = anomalous / total if total else 0

    logger.info(f"Summary: {anomalous} anomalous / {total} total (Rate: {anomaly_rate:.2%})")

# ------------------ Main Pipeline ------------------

def main() -> None:
    """Orchestrates the full anomaly detection pipeline."""
    logger.info("🚀 Starting anomaly detection pipeline...")

    # Ensure database is healthy
    test_db_connection(Config.REQUIRED_TABLES)

    # Step 1: Fetch
    transactions_df = fetch_data()
    if transactions_df is None or transactions_df.empty:
        logger.warning("No data retrieved for anomaly detection.")
        return

    # Step 2: Detect anomalies
    anomalies_df = detect_anomalies(transactions_df)

    # Step 3: Log basic summary
    log_summary(anomalies_df)

    # Step 4: Store results
    store_anomaly_results(anomalies_df)

    # Step 5: Optional - Product level anomaly aggregation
    product_anomalies_df = aggregate_product_anomalies(anomalies_df)
    logger.info(f"Aggregated anomalies at product level for {len(product_anomalies_df)} products.")

    logger.info("🏁 Anomaly detection pipeline completed successfully.")

if __name__ == "__main__":
    main()
