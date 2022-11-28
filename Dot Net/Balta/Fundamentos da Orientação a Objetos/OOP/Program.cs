using System;

namespace OOP
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello World!");
        }
    }

    class Payment
    {
        DateTime Vencimento;

        public int MyProperty { get; set; }
        private int myVar;

        private int myVar2;
        public int MyProperty2
        {
            get { return myVar2; }
            set { myVar2 = value; }
        }

        public Payment()
        {
            Vencimento = DateTime.Now;
            MyProperty = 2;
            MyProperty2 = 8;
        }

        void Pay()
        { }
    }
}
