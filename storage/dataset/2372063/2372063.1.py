def main():
    # hasil
    n = int(input('masukan angka : '))
    while n % 2 == 0:
        print(n,'genap')
        n = int(input('masukan angka : '))
        if n != 9999:
            print (n,'selesai')
        else:
            print(n,'ganjil')   
        
            

main()