import sys
import hashlib
from urllib.parse import urlencode

def generate_payment_url(data, sign, base_url):
    query_string = urlencode(data)
    return f"{base_url}?{query_string}&sign={sign}"

if __name__ == '__main__':
    
    # Получение данных из аргументов командной строки
    str_data = sys.argv[1]
    base_payment_url = "https://tegro.money/pay"

    # Разбивка строки данных на словарь
    data = dict(item.split('=') for item in str_data.split('&'))

    # Проверка подписи
    secret = 'your_secret_key'  # Замените на свой secret key
    sorted_data = sorted(data.items())
    data_string = urlencode(sorted_data)
    sign = hashlib.md5((data_string + secret).encode()).hexdigest()
    payment_url = generate_payment_url(data, sign, base_payment_url)
    print(payment_url)


