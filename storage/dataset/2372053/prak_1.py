def main():
    #int n
    n = int(input('pengulangan: '))
    for i in range (n):
        angka = int(input('angka: '))
        if angka % 2:
            print('ganjil')
        else:
            print('genap')
    
main()