def main():
    n = int(input("N: "))
    while(n!=0):
        angka = int(input())
        if (angka % 2 == 1):
            print("bukan genap")
        elif (angka % 2 == 0):
            print("genap")    
        n = n -1
main()