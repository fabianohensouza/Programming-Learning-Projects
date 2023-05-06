using System;
using System.Text;

namespace StringBuild // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var text = new StringBuilder();
            text.Append("This Text Is a Test ");
            text.Append("of the method Append ");
            text.Append("\n");
            text.Append("provided by .NET");

            Console.WriteLine(text.ToString());
        }
    }
}