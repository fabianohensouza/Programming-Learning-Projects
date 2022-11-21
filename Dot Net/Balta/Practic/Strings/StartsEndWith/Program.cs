using System;

namespace StartsEndWith // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = "This text is a test";

            Console.WriteLine(text.StartsWith("This")); //True
            Console.WriteLine(text.StartsWith("this")); //False
            Console.WriteLine(text.StartsWith("this", StringComparison.OrdinalIgnoreCase)); //True

            Console.WriteLine("****");

            Console.WriteLine(text.EndsWith("test")); //True
            Console.WriteLine(text.EndsWith("Test")); //False
            Console.WriteLine(text.EndsWith("Test", StringComparison.OrdinalIgnoreCase)); //True
        }
    }
}
