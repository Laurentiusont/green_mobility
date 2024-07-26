def main():
    #int n
    n = int(input('Masukkan n : '))
    
    for i in range(n):
        angka = int(input('Masukkan angka : '))
        if (angka % 2 == 0):
            print( 'genap')
        else:
            print('bukan genap')
main()