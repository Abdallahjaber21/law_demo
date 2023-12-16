from flask import Flask, request, jsonify
import nltk
from nltk.tokenize import word_tokenize, sent_tokenize
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import re

nltk.download('punkt')
nltk.download('stopwords')
nltk.download('wordnet')

app = Flask(__name__)

@app.route('/', methods=['POST'])
def answer_question():
    data = request.get_json()
    pdf_text = data.get('pdf_text')
    user_question = data.get('user_question')

    # Add your question-answering logic here
    answer = preprocess_text(pdf_text, user_question)

    return jsonify({'answer': answer})


def preprocess_text(text):
    # Remove lines containing copyright information and page numbers
    lines = [line for line in text.split('\n') if not re.search(r'COPYRIGHT|Page \d+', line)]

    # Combine the remaining lines into a single text
    cleaned_text = ' '.join(lines)

    # Tokenize the cleaned text
    words = word_tokenize(cleaned_text)

    # Remove stop words
    stop_words = set(stopwords.words("english"))
    words = [word for word in words if word.lower() not in stop_words]

    # Lemmatization
    lemmatizer = WordNetLemmatizer()
    words = [lemmatizer.lemmatize(word) for word in words]

    return ' '.join(words)

if __name__ == '__main__':
    app.run(debug=True)