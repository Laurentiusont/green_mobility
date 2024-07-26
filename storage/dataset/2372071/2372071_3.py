def main():
    # int n
    n = int(input("n: "))

    # int count_prima
    count_prima = 0
    if (n == 2 or n == 3):
        print("prima")
    elif (n != 0 and n != 1):
        if (n / n == 1 and n / 1 == n):
            if ((n % 2 == 0 or n % 3 == 0)):
                print("bukan prima")
            else:
                print("prima")
    else:
        print("bukan prima")
    
main()