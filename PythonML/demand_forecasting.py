import os
import logging
from datetime import timedelta, datetime
from typing import Optional, List

import pandas as pd
import holidays
import matplotlib.pyplot as plt
from sqlalchemy import create_engine, text
from lightgbm import LGBMRegressor, early_stopping, log_evaluation
from sklearn.model_selection import train_test_split
from db import get_engine, test_db_connection

# ------------------ Configuration ------------------
REQUIRED_TABLES = {'sales', 'products'}
SAFETY_DAYS = 5
FORECAST_DAYS = 30
MIN_TRAINING_SAMPLES = 60

# Set up logging
logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

# ------------------ Database Utilities ------------------

engine = get_engine()

def fetch_sales_data() -> Optional[pd.DataFrame]:
    """Fetch sales and product information from database."""
    query = """
        SELECT s.product_id, s.quantity, s.sale_date, p.name AS product_name, p.quantity_in_stock
        FROM sales s
        JOIN products p ON s.product_id = p.id
        ORDER BY s.sale_date
    """
    try:
        df = pd.read_sql(query, engine)
        if df.empty:
            logger.warning("⚠️ No sales data found.")
            return None
        logger.info(f"✅ Retrieved {len(df)} sales records.")
        return df
    except Exception as e:
        logger.error(f"Failed to fetch sales data: {e}")
        return None

# ------------------ Data Processing ------------------

def get_philippine_holidays(years: List[int]) -> pd.DatetimeIndex:
    """Get Philippine holidays for the given years."""
    return pd.to_datetime(list(holidays.PH(years=years).keys()))

def preprocess_data(df: pd.DataFrame) -> Optional[pd.DataFrame]:
    """Perform feature engineering on sales data."""
    try:
        df["sale_date"] = pd.to_datetime(df["sale_date"])
        years = df["sale_date"].dt.year.unique()
        ph_holidays = get_philippine_holidays(years)

        df = df.assign(
            day_of_week=df["sale_date"].dt.dayofweek,
            month=df["sale_date"].dt.month,
            week_of_year=df["sale_date"].dt.isocalendar().week,
            is_weekend=(df["sale_date"].dt.dayofweek >= 5).astype(int),
            is_holiday=df["sale_date"].isin(ph_holidays).astype(int),
            is_school_season=df["sale_date"].dt.month.isin([6,7,8,9,10,11,12,1,2,3]).astype(int),
            is_christmas=df["sale_date"].dt.month.isin([11,12]).astype(int),
            is_summer=df["sale_date"].dt.month.isin([4,5]).astype(int)
        )

        # Rolling mean features
        df["rolling_mean_7"] = df.groupby('product_id')["quantity"].transform(lambda x: x.rolling(7, min_periods=1).mean())
        df["rolling_mean_30"] = df.groupby('product_id')["quantity"].transform(lambda x: x.rolling(30, min_periods=1).mean())

        # Lag features based on data availability
        counts = df.groupby("product_id")["sale_date"].count()
        min_count = counts.min()
        lag_days = [7, 14, 30] if min_count >= 30 else ([7, 14] if min_count >= 14 else [7])

        for lag in lag_days:
            df[f"lag_{lag}"] = df.groupby('product_id')["quantity"].shift(lag)

        df.dropna(subset=[f"lag_{lag}" for lag in lag_days], inplace=True)

        # Product-level mean quantity
        product_mean = df.groupby('product_id')["quantity"].mean()
        df["product_mean"] = df["product_id"].map(product_mean)

        if df.empty:
            logger.warning("⚠️ No data left after preprocessing.")
            return None

        logger.info(f"✅ Preprocessed {len(df)} rows with engineered features.")
        return df
    except Exception as e:
        logger.error(f"Preprocessing failed: {e}")
        return None

# ------------------ Modeling ------------------

def train_model(X_train: pd.DataFrame, y_train: pd.Series, X_val: Optional[pd.DataFrame] = None, y_val: Optional[pd.Series] = None) -> LGBMRegressor:
    """Train LGBMRegressor model."""
    model = LGBMRegressor(
        n_estimators=500,
        learning_rate=0.05,
        max_depth=7,
        subsample=0.8,
        colsample_bytree=0.8,
        random_state=42
    )

    callbacks = []
    if X_val is not None and y_val is not None:
        callbacks = [early_stopping(stopping_rounds=50), log_evaluation(period=50)]
        model.fit(X_train, y_train, eval_set=[(X_val, y_val)], callbacks=callbacks)
        val_rmse = ((model.predict(X_val) - y_val) ** 2).mean() ** 0.5
        logger.info(f"Validation RMSE = {val_rmse:.2f}")
    else:
        model.fit(X_train, y_train)
        logger.info(f"Trained without validation set.")

    return model

# ------------------ Forecasting ------------------

def train_and_forecast(product_df: pd.DataFrame, product_id: int, lag_days: List[int]) -> Optional[pd.DataFrame]:
    """Train model per product and generate forecasts."""
    if len(product_df) < MIN_TRAINING_SAMPLES:
        logger.warning(f"⚠️ Insufficient data for product {product_id}. Skipping.")
        return None

    features = [
        "day_of_week", "month", "week_of_year", "is_weekend", "is_holiday",
        "is_school_season", "is_christmas", "is_summer",
        "rolling_mean_7", "rolling_mean_30", "product_mean"
    ] + [f"lag_{lag}" for lag in lag_days]

    X = product_df[features]
    y = product_df["quantity"]

    split_date = product_df["sale_date"].quantile(0.8)
    train_df = product_df[product_df["sale_date"] <= split_date]
    val_df = product_df[product_df["sale_date"] > split_date]

    if train_df.empty or val_df.empty:
        logger.warning(f"⚠️ Not enough data to split for product {product_id}. Training on full data.")
        train_df, val_df = product_df, None

    model = train_model(
        X_train=train_df[features],
        y_train=train_df["quantity"],
        X_val=val_df[features] if val_df is not None else None,
        y_val=val_df["quantity"] if val_df is not None else None
    )

    # Forecasting
    today = datetime.now().date()
    forecast_start_date = today + timedelta(days=30)  # Start forecasting from 30 days from today
    forecast_dates = pd.date_range(forecast_start_date, periods=FORECAST_DAYS)
    history = product_df.copy()
    forecasts = []

    for forecast_date in forecast_dates:
        feature_row = build_forecast_features(forecast_date, history, product_df, lag_days)
        predicted_quantity = max(0, model.predict(pd.DataFrame([feature_row]))[0])

        forecasts.append({
            "product_id": product_id,
            "forecast_date": forecast_date,
            "predicted_quantity": predicted_quantity
        })

        # Update history with the forecasted value
        history = pd.concat([history, pd.DataFrame({
            "sale_date": [forecast_date],
            "quantity": [predicted_quantity]
        })], ignore_index=True)

    return pd.DataFrame(forecasts)

def build_forecast_features(forecast_date: pd.Timestamp, history: pd.DataFrame, base_df: pd.DataFrame, lag_days: List[int]) -> dict:
    """Build feature set for a single forecast date."""
    feature_row = {
        "day_of_week": forecast_date.dayofweek,
        "month": forecast_date.month,
        "week_of_year": forecast_date.isocalendar().week,
        "is_weekend": int(forecast_date.dayofweek >= 5),
        "is_holiday": int(forecast_date in get_philippine_holidays([forecast_date.year])),
        "is_school_season": int(forecast_date.month in [6,7,8,9,10,11,12,1,2,3]),
        "is_christmas": int(forecast_date.month in [11,12]),
        "is_summer": int(forecast_date.month in [4,5]),
        "rolling_mean_7": history["quantity"].rolling(7, min_periods=1).mean().iloc[-1],
        "rolling_mean_30": history["quantity"].rolling(30, min_periods=1).mean().iloc[-1],
        "product_mean": base_df["quantity"].mean()
    }

    for lag in lag_days:
        feature_row[f"lag_{lag}"] = history["quantity"].iloc[-lag] if len(history) >= lag else history["quantity"].iloc[0]

    return feature_row

# ------------------ Business Logic ------------------

def generate_restock_recommendations(forecast_df: pd.DataFrame, stock_df: pd.DataFrame) -> Optional[pd.DataFrame]:
    """Generate reorder recommendations based on forecasted demand."""
    try:
        demand_summary = forecast_df.groupby("product_id")["predicted_quantity"].sum().reset_index()
        merged = demand_summary.merge(stock_df, on="product_id", how="left")

        merged["avg_daily_demand"] = merged["predicted_quantity"] / FORECAST_DAYS
        merged["safety_stock"] = merged["avg_daily_demand"] * SAFETY_DAYS
        merged["projected_stock"] = merged["quantity_in_stock"] - merged["predicted_quantity"]

        # Update the reorder threshold calculation
        merged["reorder_threshold"] = merged["avg_daily_demand"] * (SAFETY_DAYS + FORECAST_DAYS)

        # Then calculate reorder quantity based on the reorder threshold and current stock
        merged["reorder_quantity"] = merged.apply(
            lambda row: max(0, row["reorder_threshold"] - row["quantity_in_stock"]),
            axis=1
        )

        logger.info(f"✅ Generated restocking recommendations for {len(merged)} products.")
        return merged[["product_id", "predicted_quantity", "quantity_in_stock", "projected_stock", "reorder_quantity"]]
    except Exception as e:
        logger.error(f"Failed to generate restocking recommendations: {e}")
        return None

def update_product_suggestions(recommendations: pd.DataFrame):
    """Update products table with suggested reorder threshold, safety stock, and forecast update date."""
    try:
        with engine.begin() as conn:
            for _, row in recommendations.iterrows():
                # Calculate Suggested Reorder Threshold (reorder):
                avg_daily_demand = row["total_forecasted_demand"] / FORECAST_DAYS  # Updated column name
                safety_stock = avg_daily_demand * SAFETY_DAYS
                reorder = (avg_daily_demand * FORECAST_DAYS) + safety_stock

                # Execute the update for the product
                conn.execute(text("""
                    UPDATE products
                    SET
                        suggested_reorder_threshold = :reorder,
                        suggested_safety_stock = :safety,
                        last_forecast_update = :updated_at
                    WHERE id = :product_id
                """), {
                    "reorder": int(round(reorder)),  # Suggested reorder threshold
                    "safety": int(round(safety_stock)),  # Suggested safety stock
                    "updated_at": datetime.now(),
                    "product_id": int(row["product_id"])
                })
        logger.info("✅ Updated products with forecast suggestions.")
    except Exception as e:
        logger.error(f"❌ Failed to update product suggestions: {e}")

# ------------------ Main Execution ------------------

def main() -> None:
    logger.info("🚀 Starting demand forecasting process...")

    test_db_connection(REQUIRED_TABLES)

    sales_data = fetch_sales_data()
    if sales_data is None:
        return

    processed_data = preprocess_data(sales_data)
    if processed_data is None:
        return

    lag_days = [int(col.split("_")[1]) for col in processed_data.columns if col.startswith("lag_")]
    forecasts = []

    for product_id in processed_data["product_id"].unique():
        logger.info(f"🔄 Forecasting for product_id={product_id}...")
        forecast = train_and_forecast(processed_data[processed_data["product_id"] == product_id], product_id, lag_days)
        if forecast is not None:
            forecast["predicted_quantity"] = forecast["predicted_quantity"].round(2)
            forecast["created_at"] = datetime.now()
            forecast["updated_at"] = datetime.now()
            forecasts.append(forecast)
            logger.info(f"✅ Forecast completed for product_id={product_id}.")

    if not forecasts:
        logger.warning("⚠️ No forecasts generated.")
        return

    final_forecast = pd.concat(forecasts, ignore_index=True)

    # Delete previous forecast data from the demand_forecasts table
    try:
        with engine.begin() as conn:
            conn.execute(text("DELETE FROM demand_forecasts"))
        logger.info("✅ Deleted previous forecast data from demand_forecasts table.")
    except Exception as e:
        logger.error(f"❌ Failed to delete previous demand forecast data: {e}")
        return

    # Now insert the new forecast data into the demand_forecasts table
    try:
        final_forecast.to_sql("demand_forecasts", engine, if_exists="append", index=False)
        logger.info(f"✅ Inserted {len(final_forecast)} rows into demand_forecasts.")
    except Exception as e:
        logger.error(f"❌ Failed to save demand forecasts: {e}")

    # Get current stock levels
    stock_df = sales_data[["product_id", "quantity_in_stock"]].drop_duplicates()

    recommendations = generate_restock_recommendations(final_forecast, stock_df)
    if recommendations is not None and not recommendations.empty:
        recommendations.rename(columns={
            "predicted_quantity": "total_forecasted_demand"
        }, inplace=True)
        recommendations["created_at"] = datetime.now()
        recommendations["updated_at"] = datetime.now()

        try:
            recommendations.to_sql("restocking_recommendations", engine, if_exists="append", index=False)
            logger.info("✅ Restocking recommendations saved.")
            update_product_suggestions(recommendations)
        except Exception as e:
            logger.error(f"❌ Failed to save restocking recommendations: {e}")
    else:
        logger.warning("⚠️ No recommendations to save.")

    logger.info("🏁 Forecasting pipeline completed successfully.")

if __name__ == "__main__":
    main()
