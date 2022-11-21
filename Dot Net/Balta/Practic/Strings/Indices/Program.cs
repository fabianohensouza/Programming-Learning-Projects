using System;

namespace Indices // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = "This text is a test";

            Console.WriteLine(text.IndexOf("text"));
            Console.WriteLine(text.IndexOf("x"));
            Console.WriteLine(text.IndexOf("s"));
            Console.WriteLine(text.LastIndexOf("s"));
        }
    }
}
