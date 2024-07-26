def main():
    # int jumlah_suku
    jumlah_suku = int(input('jumlah suku : '))
    # int awal
    awal = int(input('awal : '))
    # int inkremen
    inkremen = int(input('inkremen : '))
    # str operasi
    operasi = input('operasi : (+,-,*) : ')
    # int jumlah_deret
    jumlah_deret = 0
    if operasi == '+':
        for i in range(0, jumlah_suku, 1):
            jumlah_deret += awal
            print(awal, end=" + ")
            awal += inkremen
    elif operasi == '-':
        for i in range(0, jumlah_suku, 1):
            jumlah_deret -= awal
            print(awal, end=' - ')
            awal += inkremen
    elif operasi == '*':
        jumlah_deret = 1
        for i in range(0, jumlah_suku, 1):
            jumlah_deret *= awal
            print(awal, end=" * ")
            awal += inkremen
    print(f"jumlah deret {jumlah_deret}")
main()