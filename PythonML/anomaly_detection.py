import os
import logging
import pandas as pd
from sqlalchemy import text
from sklearn.ensemble import IsolationForest
from sklearn.preprocessing import StandardScaler
from sklearn.decomposition import PCA
from db import get_engine, test_db_connection
from datetime import datetime
from typing import Optional, List

# ------------------ Configuration ------------------

REQUIRED_TABLES = {'transactions', 'products'}

# Hyperparameters (can also be moved to .env or external config later)
CONTAMINATION_RATE = 0.05
RANDOM_STATE = 42
BATCH_SIZE = 1000  # For large inserts
USE_PCA = False    # Toggle for dimensionality reduction

# Expected Columns for anomaly_detection_results table
EXPECTED_COLUMNS = [
    'transaction_id', 'product_id', 'anomaly_score', 'status', 'created_at', 'updated_at'
]

# Set up logging
logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

# Create database engine
engine = get_engine()

# ------------------ Anomaly Status ------------------

class AnomalyStatus:
    Normal = 'normal'
    Anomalous = 'anomalous'

# ------------------ Database Utilities ------------------

def fetch_data() -> Optional[pd.DataFrame]:
    """Fetch transactions and product data from the database."""
    query = """
        SELECT t.id AS transaction_id, t.product_id, t.quantity, t.type, p.price, t.created_at, t.created_by
        FROM transactions t
        JOIN products p ON t.product_id = p.id
    """
    try:
        data_df = pd.read_sql(query, engine)
        logger.info(f"✅ Retrieved {len(data_df)} transaction records.")
        return data_df
    except Exception as e:
        logger.error(f"❌ Failed to fetch data: {e}")
        return None

# ------------------ Feature Engineering ------------------

def feature_engineering(data_df: pd.DataFrame) -> pd.DataFrame:
    """Perform feature engineering for anomaly detection."""
    # Defensive copy
    data_df = data_df.copy()

    # Replace missing 'created_by' with 0
    data_df['user_id'] = data_df['created_by'].fillna(0)

    # Transaction value
    data_df['transaction_value'] = data_df['quantity'] * data_df['price']

    # Extract time features
    data_df['created_at'] = pd.to_datetime(data_df['created_at'])
    data_df['hour'] = data_df['created_at'].dt.hour
    data_df['day_of_week'] = data_df['created_at'].dt.dayofweek

    # Transaction type encoding
    data_df['transaction_type'] = data_df['type'].astype('category').cat.codes

    return data_df

# ------------------ Anomaly Detection ------------------

def detect_anomalies(data_df: pd.DataFrame) -> pd.DataFrame:
    """Detect anomalies in transaction data using Isolation Forest."""
    data_df = feature_engineering(data_df)

    feature_cols = ['quantity', 'transaction_value', 'hour', 'day_of_week', 'transaction_type']

    # Standardize features
    scaler = StandardScaler()
    scaled_features = scaler.fit_transform(data_df[feature_cols])

    # Dimensionality reduction (optional)
    features = scaled_features
    if USE_PCA:
        pca = PCA(n_components=2)
        features = pca.fit_transform(scaled_features)
        logger.info(f"ℹ️ Applied PCA dimensionality reduction.")

    # Isolation Forest for anomaly detection
    isolation_forest = IsolationForest(contamination=CONTAMINATION_RATE, random_state=RANDOM_STATE)
    data_df['anomaly_score'] = isolation_forest.fit_predict(features)

    # Human-readable anomaly status
    data_df['status'] = data_df['anomaly_score'].apply(
        lambda x: AnomalyStatus.Anomalous if x == -1 else AnomalyStatus.Normal
    )

    logger.info(f"✅ Anomaly detection completed for {len(data_df)} transactions.")

    return data_df[['transaction_id', 'product_id', 'anomaly_score', 'status']]

# ------------------ Database Update ------------------

def validate_columns(df: pd.DataFrame, expected_cols: List[str]) -> bool:
    """Validate that DataFrame has expected columns before inserting into database."""
    missing_cols = set(expected_cols) - set(df.columns)
    if missing_cols:
        logger.error(f"❌ DataFrame is missing columns for insert: {missing_cols}")
        return False
    return True

def update_anomaly_detection_results(df: pd.DataFrame) -> None:
    """Store the anomaly detection results in the database."""
    current_time = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    df = df.copy()
    df['created_at'] = current_time
    df['updated_at'] = current_time

    if not validate_columns(df, EXPECTED_COLUMNS):
        logger.error("❌ Aborting database insert due to schema mismatch.")
        return

    try:
        with engine.begin() as conn:
            insert_data = df.to_dict(orient='records')
            stmt = text("""
                INSERT INTO anomaly_detection_results
                (transaction_id, product_id, anomaly_score, status, created_at, updated_at)
                VALUES (:transaction_id, :product_id, :anomaly_score, :status, :created_at, :updated_at)
            """)
            for i in range(0, len(insert_data), BATCH_SIZE):
                batch = insert_data[i:i+BATCH_SIZE]
                conn.execute(stmt, batch)

        logger.info(f"✅ Stored anomaly detection results for {len(df)} transactions in batches.")
    except Exception as e:
        logger.error(f"❌ Failed to store anomaly detection results: {e}")

# ------------------ Metrics/Monitoring ------------------

def log_detection_summary(anomalies_df: pd.DataFrame) -> None:
    """Log a basic summary of anomaly detection results."""
    total = len(anomalies_df)
    anomalous = (anomalies_df['status'] == AnomalyStatus.Anomalous).sum()
    normal = total - anomalous
    anomaly_rate = anomalous / total if total > 0 else 0

    logger.info(f"📊 Detection Summary: {anomalous} anomalies, {normal} normal, anomaly rate = {anomaly_rate:.2%}")

# ------------------ Main Execution ------------------

def main() -> None:
    """Main function to run the anomaly detection process."""
    logger.info("🚀 Starting fraud/anomaly detection process...")

    test_db_connection(REQUIRED_TABLES)

    data_df = fetch_data()
    if data_df is None or data_df.empty:
        logger.warning("⚠️ No data available for anomaly detection.")
        return

    anomalies_df = detect_anomalies(data_df)
    log_detection_summary(anomalies_df)
    update_anomaly_detection_results(anomalies_df)

    logger.info("🏁 Fraud/Anomaly detection process completed successfully.")

if __name__ == "__main__":
    main()
