import sys
import cv2
import pytesseract

# Путь к Tesseract (адаптируй под сервер)
pytesseract.pytesseract.tesseract_cmd = r'E:\Tesseract\tesseract.exe'  # Для Windows, на сервере изменится

def perform_ocr(image_path, psm_value=6):
    # Чтение изображения
    image = cv2.imread(image_path)
    if image is None:
        return "Ошибка: не удалось загрузить изображение"

    # Преобразование в оттенки серого
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # Бинаризация для улучшения качества
    _, binary = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Настройка Tesseract с PSM (0-13)
    config = f'--psm {psm_value}'
    text = pytesseract.image_to_string(binary, lang='eng+rus', config=config)

    return text

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Ошибка: укажи путь к изображению")
        sys.exit(1)

    image_path = sys.argv[1]
    # PSM как необязательный аргумент (по умолчанию 6)
    psm_value = int(sys.argv[2]) if len(sys.argv) > 2 else 6
    if psm_value < 0 or psm_value > 13:
        print("Ошибка: PSM должен быть от 0 до 13")
        sys.exit(1)

    text = perform_ocr(image_path, psm_value)
    print(text)