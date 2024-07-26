# File : 3.py 
# Penulis : Christofer Eric
# Tujuan Program : membuat program membaca hitungan prima atau bukan prima
# Kamus Data
def main():
    # integer n
    n = int(input("Angka : "))
    # proses
    if (n <= 1):
        print("Bukan Prima")
    else:
        is_prime = True
    # loop i
        for i in range(2,n):
            if (n % i == 0):
                is_prime = False
                break
    # output
        if (is_prime):
            print("Prima")
        else:
            print("Bukan Prima")
            
    return 0
 
if __name__ == '__main__':    
    main()   