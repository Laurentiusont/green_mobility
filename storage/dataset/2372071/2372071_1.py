def main():
    # int jumlah_suku
    jumlah_suku = int(input("N: "))

    for i in range(0, jumlah_suku):
        # int angka
        angka = int(input(""))

        if (angka % 2 == 0):
            print("Genap")
        elif (angka % 2 != 0):
            print("Ganjil")
main()