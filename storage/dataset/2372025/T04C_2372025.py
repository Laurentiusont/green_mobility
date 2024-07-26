def main():
    a = int(input("Angka : "))
    for i in range (1, a +1,1):
        if (a == 3):
            print("Prima")
        elif (a == 5):
            print("Prima")
        elif (a == 7):
            print("Prima")
        elif (a % 2 == 0):
            print ("Bukan Prima")
        elif (a % 3 == 0):
            print ("Bukan Prima")
        elif (a % 5 == 0):
            print ("Bukan Prima")
        elif (a % 7 == 0):
            print ("Bukan Prima")
        else:
            print ("Prima")
        a = int(input("Angka : "))
    return 0
    
 
main()