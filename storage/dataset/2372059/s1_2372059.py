def main():
    while True:
        #int N
        n = int(input('N :'))
        if (n == 9999):
            print(" ")
        if (n%2==0):
            print(f"{n}/Genap")
        else:
            print(f"{n}/Bukan Genap")
main()