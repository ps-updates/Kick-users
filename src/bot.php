<?php

// Fetch from environment variables
$botToken = getenv('BOT_TOKEN');
$webhookUrl = getenv('WEBHOOK_URL');

// Get update data from Telegram
$update = file_get_contents('php://input');
$updateData = json_decode($update, true);

if (isset($updateData['message'])) {
    $chatId = $updateData['message']['chat']['id'];
    $userId = $updateData['message']['from']['id'];
    $messageText = $updateData['message']['text'];

    if ($messageText === '/start') {
        $welcomeMessage = "Hello " . $updateData['message']['from']['first_name'] . "! 👋\n\n";
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

        file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?" . http_build_query($apiRequest));
    }
}

// Ban user if they leave the channel
if (isset($updateData['chat_member']) && $updateData['chat_member']['new_chat_member']['status'] === 'left') {
    $chatId = $updateData['chat_member']['chat']['id'];
    $userId = $updateData['chat_member']['from']['id'];

    file_get_contents("https://api.telegram.org/bot$botToken/banChatMember?chat_id=$chatId&user_id=$userId");
}

// Set Webhook
$setWebhookRequest = [
    'url' => $webhookUrl,
    'allowed_updates' => json_encode(['message', 'chat_member'])
];

file_get_contents("https://api.telegram.org/bot$botToken/setWebhook?" . http_build_query($setWebhookRequest));

?>
