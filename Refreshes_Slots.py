import time
import requests
from datetime import datetime
import os
from dotenv import load_dotenv

def send_telegram_notification(bot_token, chat_id, message):
    url = f"https://api.telegram.org/bot{bot_token}/sendMessage"
    data = {
        'chat_id': chat_id,
        'text': message
    }
    response = requests.post(url, data=data)
    if response.status_code != 200:
        print(f"Failed to send message: {response.text}")

def fetch_slots(location_id, limit, start_date, end_date):
    url = f"https://ttp.cbp.dhs.gov/schedulerapi/slots?orderBy=soonest&limit={limit}&locationId={location_id}&minimum=1"
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error fetching slots: {e}")
        return None

def main():
    limit = 10
    location_id = 5021  
    load_dotenv()
    telegram_bot_token = os.getenv("TELEGRAM_BOT_TOKEN")
    telegram_chat_id = os.getenv("TELEGRAM_CHAT_ID")
 
    #CHANGE TO THE DATES YOU NEED
    start_date = datetime.strptime('2024-12-30', '%Y-%m-%d')
    end_date = datetime.strptime('2025-01-03 23:59:59', '%Y-%m-%d %H:%M:%S')

    while True:   
        print("Checking available slots...")
        slots = fetch_slots(location_id, limit, start_date, end_date)

        if slots:
            availability_found = False
            notification_message = "Available Slots:\n"

            for slot in slots:
                start_timestamp = datetime.fromisoformat(slot['startTimestamp'].replace('Z', ''))

                if start_date <= start_timestamp <= end_date and start_timestamp.hour >= 12:
                    availability_found = True
                    formatted_date = start_timestamp.strftime("%Y-%m-%d %H:%M:%S")
                    print(f"Date: {formatted_date}")
                    notification_message += f"- Date: {formatted_date}\n"

            if availability_found:
                send_telegram_notification(telegram_bot_token, telegram_chat_id, notification_message)
            else:
                print("No slots available in the specified date range.")
        else:
            print("No slots available or unable to fetch data.")

        print("Refreshing in 1 minute...")
        time.sleep(60)

if __name__ == "__main__":
    main()
