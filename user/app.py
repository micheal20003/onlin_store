from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib

app = Flask(__name__)
CORS(app)  # Enable CORS for web access

# Load the trained BMR model
bmr_model = joblib.load("bmr_model.pkl")

@app.route("/predict_bmr", methods=["POST"])
def predict_bmr():
    try:
        data = request.get_json()
        age = int(data["age"])
        gender = data["gender"]
        height = float(data["height"])
        weight = float(data["weight"])
        activity_factor = float(data["activity_factor"])

        # BMR Formula (Harris-Benedict Equation)
        if gender == "male":
            predicted_bmr = 88.36 + (13.4 * weight) + (4.8 * height) - (5.7 * age)
        else:
            predicted_bmr = 447.6 + (9.2 * weight) + (3.1 * height) - (4.3 * age)

        # Estimate daily calorie needs
        daily_calories = predicted_bmr * activity_factor

        return jsonify({"BMR": predicted_bmr, "Daily_Calories": daily_calories})
    
    except Exception as e:
        return jsonify({"error": str(e)})

if __name__ == "__main__":
    app.run(debug=True)
