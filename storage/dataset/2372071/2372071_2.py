def main():
    # int jumlah_suku
    jumlah_suku = int(input("jumlah suku: "))

    # int awal
    awal = int(input("awal: "))

    # int increment
    increment = int(input("increment: "))

    # string operasi
    operasi = input("operasi: ")
    
    # int jumlah_deret
    jumlah_deret = awal
    
    if (operasi == "+"):
        for i in range(0, jumlah_suku):
            if (i != jumlah_suku - 1):
                print(f"{awal}+", end="")
            else:
                print(f"{awal}")
            
            awal += increment
            jumlah_deret = jumlah_deret + awal
    elif (operasi == "*"):
        for i in range(0, jumlah_suku):
            if (i != jumlah_suku - 1):
                print(f"{awal}*", end="")
            else:
                print(f"{awal}")
            
            awal += increment
            jumlah_deret = jumlah_deret * awal
    elif (operasi == "-"):
        for i in range(0, jumlah_suku):
            if (i != jumlah_suku - 1):
                print(f"{awal}-", end="")
            else:
                print(f"{awal}")
            
            awal -= increment
            jumlah_deret = jumlah_deret - awal
    print(f"Jumlah deret: {jumlah_deret}")
main()