# NexusChecker

NexusChecker is a project for monitoring and notifying about available appointment slots for Global Entry Enrollment Centers. It includes both a **Python script** for automatic notifications via Telegram and a **PHP web form** for manual slot checking.

---

## Features

### Python Script
- Fetches appointment slots from the [TTP Scheduler API](https://ttp.cbp.dhs.gov/schedulerapi).
- Sends Telegram notifications for available slots within a specified date range.
- Refreshes automatically every minute.
- Easy configuration using a `.env` file for sensitive information.

### PHP Web Form
- Provides a web interface for manually checking appointment availability.
- Allows users to select an enrollment center and view available slots within a specific date range.

---

## Requirements

### For Python
- Python 3.7+
- A Telegram bot created via [BotFather](https://core.telegram.org/bots#botfather).
- A valid chat ID where notifications will be sent.

### For PHP
- PHP 7.4+ with `file_get_contents` enabled.
- A web server (e.g., Apache or Nginx) to host the form.

---

## Installation

### Python Script

1. Set up the .env file: Create a .env file in the root directory with the following content:
   ```bash
    TELEGRAM_BOT_TOKEN=your_bot_token_here
    TELEGRAM_CHAT_ID=your_chat_id_here

### PHP Web Form
1. Deploy the index.php file: Place the PHP file on your web server (e.g., under /var/www/html for Apache or the appropriate directory for your setup).

2. Edit the PHP script: Update the $telegram_bot_token and $telegram_chat_id variables in the script with your Telegram bot credentials.

3. Access the form: Open the PHP file in your browser (e.g., http://localhost/NexusChecker/index.php).

---

## Configuration

### Python Script
- Enrollment Centers: Modify the enrollment_centers dictionary in the script to include or remove enrollment centers as needed.
- Default Location ID: Change the location_id variable in the script to set your preferred default enrollment center.
- Date Range: Update the start_date and end_date variables in the script to define the range of dates to check for appointments.

### PHP Form
- Enrollment Centers: Update the $enrollment_centers array in the PHP script to include or remove enrollment centers.
- API URL: The PHP script fetches slots dynamically from the TTP Scheduler API.

---

## Example Outputs

### Python Script
The script prints available slots to the console and sends the first 10 slots to your Telegram chat:

      Checking available slots...
      Date: 2025-02-11 08:50:00
      Date: 2025-02-11 09:10:00
      Date: 2025-02-11 09:40:00
      ...
      Refreshing in 1 minute...

---

## Notes
- The Telegram API has a message size limit of 4096 characters. The Python script handles this by sending only the first 10 slots.
- Ensure the PHP script's server has internet access to make API requests.

---

## Contributing
Feel free to fork this repository and submit pull requests for improvements or additional features.
