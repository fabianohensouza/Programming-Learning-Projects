using System;
using Microsoft.Data.SqlClient;

namespace DataAccess // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            const string connectionString = "Server=localhost,1433;Database=balta;User ID=sa;Password=1q2w3e4r@#$";
            Console.WriteLine("Hello World!");
        }
    }
}