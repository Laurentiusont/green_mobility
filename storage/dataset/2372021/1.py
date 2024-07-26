def main():
    n = int(input("Masukan angka : "))
    for x in range (1,n+1,1):
        a = int(input(" Masukan angka "))
        if (a % 2 == 0):
            print ("Genap")
        else:
            print ("Ganjil")        
main()