def main():
    # int n
    n = int(input())
    for i in range (0, n):
        # int angka
        angka = int(input('angka : '))
        if(angka % 2 == 0):
            print('genap')
        else:
            print('bukan genap')
        
main()