<?php

$enrollment_centers = [
    8040 => "Albuquerque Enrollment Center - Albuquerque International Sunport 2200 Sunport Blvd SE Albuquerque NM 87106",
    6580 => "American Express - New York - 0 UNAVAILABLE NEW YORK NY 99999 US",
    7540 => "Anchorage Enrollment Center - Ted Stevens International Airport 4600 Postmark Drive RM NA 207 Anchorage",
    5182 => "Atlanta International Global Entry EC - 2600 Maynard H. Jackson Jr. Int'l Terminal Maynard H. Jackson Jr. Blvd.",
    5200 => "Atlanta Port Office Global Entry Enrollment Center - 157 Tradeport Drive Suite C Atlanta GA 30354 US",
    7820 => "Austin-Bergstrom International Airport - 3600 Presidential Blvd. Austin-Bergstrom International Airport",
    7940 => "Baltimore Enrollment Center - Baltimore Washington Thurgood Marshall I Lower Level Door 18 Linthicum MD 21240",
    13321 => "Blaine Global Entry Enrollment Center - 8115 Birch Bay Square St. Suite 104 Blaine WA 98230 US",
    12161 => "Boise Enrollment Center - 4655 S Enterprise Street Boise ID 83705 US",
    14221 => "Boston- Tip O'Neill Federal Building - 10 Causeway Street Room 812 Boston MA 02222 US",
    5441 => "Boston-Logan Global Entry Enrollment Center - Logan International Airport Terminal E East Boston MA 02128",
    5003 => "Brownsville Enrollment Center - 3300 South Expressway 77 83 Veterans International Bridge - Los Tomates",
    5021 => "Champlain Global Entry Enrollment Center - 237 West Service Road Champlain NY 12919 US",

    //CANADA TEST
    5028 => "Montréal International Airport",
];


$limit = 99999;
$locationId = isset($_POST['location_id']) ? $_POST['location_id'] : null; 
// Telegram Bot Details
$telegram_bot_token = '8172844554:AAF7WxzImfalKAzpJFjg2RxEjDQFlkYsVWI';
$telegram_chat_id = '-4595231898'; // Replace with your chat ID


function sendTelegramNotification($bot_token, $chat_id, $message) {
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message
    ];
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}


echo '<form method="post">';
echo '<label for="enrollment-center">Select an Enrollment Center:</label>';
echo '<select id="enrollment-center" name="location_id">';
echo '<option value="">-- Select Enrollment Center --</option>';

foreach ($enrollment_centers as $id => $name) {

    $selected = ($id == $locationId) ? 'selected' : '';
    echo "<option value=\"$id\" $selected>$name</option>";
}

echo '</select>';
echo '<button type="submit">Check Available Slots</button>';
echo '</form>';
// NOT HIS PLACE var_dump($data);
  
if ($locationId) {
    $url = "https://ttp.cbp.dhs.gov/schedulerapi/slots?orderBy=soonest&limit=$limit&locationId=$locationId&minimum=1";

    //https://ttp.cbp.dhs.gov/schedulerapi/slots?orderBy=soonest&limit=9999999&locationId=5021&minimum=1

    //$url = "https://ttp.cbp.dhs.gov/schedulerapi/slots?orderBy=soonest&limit=1&locationId=5021&minimum=1";

    $response = @file_get_contents($url);
    if ($response === FALSE) {
        echo "<p>Unable to fetch data from the API. Please try again later.</p>";
    } else {
        $data = json_decode($response, true);
        echo '<h3>Available Slots Between December 30 and January 3:</h3>';
        if ($data && is_array($data)) {
            $availability_found = false;
            $notification_message = "Available Slots:\n";

            foreach ($data as $slot) {
                $startTimestamp = strtotime($slot['startTimestamp']);
                $endTimestamp = strtotime($slot['endTimestamp']);

                $startDate = strtotime('2024-12-30');
                $endDate = strtotime('2025-01-03 23:59:59');

                if ($startTimestamp >= $startDate && $startTimestamp <= $endDate) {
                    $availability_found = true;
                    $formattedDate = date("Y-m-d H:i:s", $startTimestamp);
                    echo "Date: " . $formattedDate . "<br>";
                    $notification_message .= "- Date: $formattedDate\n";
                }
            }

            if ($availability_found) {
                sendTelegramNotification($telegram_bot_token, $telegram_chat_id, $notification_message);
            } else {
                echo "No slots available in the specified date range.";
            }
        } else {
            echo "No slots available.";
        }
    }
}
?>