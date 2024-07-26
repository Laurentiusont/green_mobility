def main():
    while True:
        a = int(input("Total: "))
    
        if(0 <= a <= 9999):
            if(a % 2 == 0 ):
                print("%i\ngenap" % a)
            else:
                print("%i\nganjil" % a)
main()  