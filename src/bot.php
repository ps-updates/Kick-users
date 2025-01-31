<?php

// Fetch from environment variables
$botToken = getenv('BOT_TOKEN');

// Set the API URL
$apiUrl = "https://api.telegram.org/bot$botToken/";

// Set the offset for polling
$offset = 0;

// Long polling loop
while (true) {
    // Fetch updates
    $updates = file_get_contents($apiUrl . "getUpdates?offset=$offset&timeout=60");
    $updateData = json_decode($updates, true);

    if (!empty($updateData['result'])) {
        foreach ($updateData['result'] as $update) {
            $offset = $update['update_id'] + 1; // Increment offset to avoid processing the same update twice

            // Process the message
            if (isset($update['message'])) {
                $chatId = $update['message']['chat']['id'];
                $userId = $update['message']['from']['id'];
                $messageText = $update['message']['text'];

                // Handle /start command
                if ($messageText === '/start') {
                    $welcomeMessage = "Hello " . $update['message']['from']['first_name'] . "! 👋\n\n";
                    $welcomeMessage .= "I am an Auto-Kicker bot. Just add me to your channel as an admin, and I will ban users who leave!";

                    $keyboard = [
                        'inline_keyboard' => [
                            [
                                ['text' => 'Add to Channel', 'url' => 'https://t.me/' . $botToken . '?startgroup=invite_to_channel']
                            ]
                        ]
                    ];

                    $apiRequest = [
                        'chat_id' => $chatId,
                        'text' => $welcomeMessage,
                        'parse_mode' => 'Markdown',
                        'reply_markup' => json_encode($keyboard)
                    ];

                    // Send the message to the user
                    file_get_contents($apiUrl . "sendMessage?" . http_build_query($apiRequest));
                }
            }

            // Handle user leaving the channel
            if (isset($update['chat_member']) && $update['chat_member']['new_chat_member']['status'] === 'left') {
                $chatId = $update['chat_member']['chat']['id'];
                $userId = $update['chat_member']['from']['id'];

                // Ban the user
                file_get_contents($apiUrl . "banChatMember?chat_id=$chatId&user_id=$userId");
            }
        }
    }
}
?>
