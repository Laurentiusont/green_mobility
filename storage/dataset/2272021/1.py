# File : 1.py
# Penulis : Christofer Eric
# Tujuan Program : Membuat program menghitung angka ganjil atau genap
# Kamus Data
def main():
    # integer n
    n = int(input("Jumlah : "))
    # loop i
    for i in range (n):
        # integer bil
        bil = int(input("Angka : "))
        if (bil % 2 == 0):
            print("Bilangan Genap")
        else:
            print("Bilangan Ganjil")
    return 0
                    
if __name__ == '__main__':    
    main()