from flask import Flask, request, jsonify
from transformers import pipeline, AutoTokenizer, AutoModelForQuestionAnswering
import nltk
from nltk.tokenize import sent_tokenize
import re
import torch  # Add this line to import the torch module

nltk.download('punkt')

app = Flask(__name__)

@app.route('/', methods=['POST'])
def answer_question():
    try:
        data = request.get_json()

        if data is None:
            raise ValueError("No JSON data provided in the request.")

        pdf_text = data.get('pdf_text')
        user_question = data.get('user_question')

        # Add your question-answering logic here
        answer = get_answer(pdf_text, user_question)

        return jsonify({'answer': answer})
    except Exception as e:
        return jsonify({'error': str(e)}), 500  # Return a 500 Internal Server Error status

def get_answer(pdf_text, user_question):
    lines = [line for line in pdf_text.split('\n') if not re.search(r'COPYRIGHT|Page \d+', line)]
    cleaned_text = ' '.join(lines)
    sentences = sent_tokenize(cleaned_text)
    paragraphs = ' '.join(sentences)

    model_name = "bert-large-uncased-whole-word-masking-finetuned-squad"
    tokenizer = AutoTokenizer.from_pretrained(model_name)
    model = AutoModelForQuestionAnswering.from_pretrained(model_name)

    # Tokenize the text into chunks that fit within the model's maximum sequence length
    max_seq_length = tokenizer.model_max_length
    chunks = [paragraphs[i:i + max_seq_length] for i in range(0, len(paragraphs), max_seq_length)]

    answers = []

    for chunk in chunks:
        inputs = tokenizer(user_question, chunk, return_tensors="pt", truncation=True)
        outputs = model(**inputs)
        answer_start = torch.argmax(outputs.start_logits)
        answer_end = torch.argmax(outputs.end_logits) + 1
        answer = tokenizer.convert_tokens_to_string(tokenizer.convert_ids_to_tokens(inputs["input_ids"][0][answer_start:answer_end]))
        answers.append(answer)

    # Combine answers from all chunks
    final_answer = ' '.join(answers)

    return final_answer

if __name__ == '__main__':
    app.run(debug=True)
