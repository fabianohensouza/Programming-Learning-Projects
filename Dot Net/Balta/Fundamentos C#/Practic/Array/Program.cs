using System;

namespace Array // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.Clear();

            //int[] meuArray = new int[5];
            //var meuArray = new int[5];
            var meuArray = new int[5] { 89, 74, 43, 14, 22 };

            var pessoa = new Pessoa() { Id = 89, Name = "Jose" };

            Console.WriteLine(meuArray[2]);

            for (int index = 0; index < meuArray.Length; index++)
                Console.WriteLine(meuArray[index]);

            Console.WriteLine("--------------------------------");

            foreach (var item in meuArray)
                Console.WriteLine(item);

            Console.WriteLine("--------------------------------");
        }

        public struct Pessoa
        {
            public int Id { get; set; }

            public string Name { get; set; }
        }
    }
}