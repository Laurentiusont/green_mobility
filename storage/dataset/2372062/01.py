def main():
    # int N
    N = int(input("N: "))
    
    while N != 9999:
        n = int(input("n: "))
        if (n % 2 == 0):
            print(f"{n} Genap")
        else:
            print(f"{n} Ganjil")

    
main()