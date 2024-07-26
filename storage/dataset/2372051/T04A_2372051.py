def main():
    # Input n
    angka = int(input("N: "))
    
    # proses
    for x in range(1, angka+1):
        nomor = int(input())
        if (nomor != 9999):
            nomor//3
            print("Bukan Genap")
                
        else:
            print("Ganjil") 
            

main()