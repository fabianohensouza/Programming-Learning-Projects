using System;

namespace GuidString // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            var id = Guid.NewGuid();

            id = new Guid("432b9b0f-6066-464c-9cd8-d99cf59e9797");
            Console.WriteLine(id);
        }
    }
}