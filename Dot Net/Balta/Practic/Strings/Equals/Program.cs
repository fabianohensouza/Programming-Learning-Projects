using System;

namespace Equals // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = "This text is a test";

            Console.WriteLine(text.Equals("This")); //False
            Console.WriteLine(text.Equals("This text is a test")); //True
            Console.WriteLine(text.Equals("this text is a test")); //False
            Console.WriteLine(text.Equals("this text is a test", StringComparison.OrdinalIgnoreCase)); //True
        }
    }
}
