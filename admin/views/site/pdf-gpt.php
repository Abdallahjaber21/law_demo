<?php
use \Smalot\PdfParser\Parser;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = "Pdf GPT";

$parser = new Parser();

$pdf = $parser->parseFile($pdf_path);

$pdfText = $pdf->getText();

$pdfText = mb_convert_encoding($pdfText, 'UTF-8');

$pdfText = iconv('UTF-8', 'UTF-8//IGNORE', $pdfText);

$json = json_encode($pdfText, JSON_UNESCAPED_UNICODE);
?>
<div class="chat-page">
    <div class="gpt-container">
        <span id="thinking-icon">🤔</span>
    </div>
    <div id="chat-container">
        <ul id="messages">
            <!-- <li class="message user-message">Hello</li> -->
            <li class="message ai-message">Hi there! I'm here to assist you.</li>
            <!-- <li class="message user-message">What's the weather like today?</li> -->
            <!-- <li class="message ai-message">The weather is currently sunny with a high of 25°C.</li> -->
        </ul>
        <div id="user-input-container">
            <input type="text" id="user-input" placeholder="Type your message...">
            <button id="send-button">➤</button>
        </div>
    </div>
</div>
<script>
    <?php ob_start(); ?>
    $(document).ready(function () {
        // Function to add a new user message and simulate AI response with delay
        function addMessage() {
            var userInput = $('#user-input');
            var messages = $('#messages');
            var gptContainer = $('.gpt-container');
            var userMessage = userInput.val();

            if (userMessage.trim() !== '') {

                // Add user message with animation
                var userMessageElement = $('<li class="message user-message">' + userMessage + '</li>').hide();
                messages.append(userMessageElement);
                userMessageElement.slideDown(200, function () {
                    // Smooth scroll to the bottom of the user message
                    messages.animate({
                        scrollTop: messages.prop("scrollHeight")
                    }, 800); // Animation duration: 800 milliseconds
                });




                // Simulate AI response with delay and blinking animation
                var aiMessage = $('<li class="message ai-message blink"></li>').hide();

                setTimeout(() => {
                    messages.append(aiMessage);
                    aiMessage.slideDown(200, function () {

                        // POST TO PYTHON
                        $.ajax({
                            url: "<?= Url::to(['site/pdf-gpt', 'pdf_path' => $pdf_path]) ?>",
                            type: "POST",
                            // contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            data: {
                                user_question: userMessage,
                                pdf_text: <?= $json ?>,
                            },
                            success: function (response) {
                                console.warn('<<< responseee >>>', response);
                            },
                            error: function (e) {
                                console.warn('<<< error: >>>', e);

                                if (e?.responseText) {
                                    fetchedResponse = e?.responseText;

                                    // Simulate fetching AI response with delay
                                    setTimeout(function () {
                                        // var fetchedResponse = "I'm sorry, I'm just a simple example. I don't have real responses.";
                                        aiMessage.removeClass('blink').text(fetchedResponse);
                                        // Smooth scroll to the bottom of the AI message
                                        messages.animate({
                                            scrollTop: messages.prop("scrollHeight")
                                        }, 800); // Animation duration: 800 milliseconds
                                    }, 800); // Delay: 800 milliseconds
                                }
                            }
                        });


                    });
                }, 500);


                // Clear user input
                userInput.val('');
            }
        }

        // Trigger addMessage function when the send button is clicked
        $('#send-button').click(function () {
            addMessage();
        });

        // Trigger addMessage function when the Enter key is pressed in the input field
        $('#user-input').keypress(function (e) {
            if (e.which === 13) {
                addMessage();
            }
        });
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>