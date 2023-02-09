using System;
using Dapper;
using DataAccess.Models;
using Microsoft.Data.SqlClient;

namespace DataAccess // Note: actual namespace depends on the project name.
{
    internal class Program
    {
        static void Main(string[] args)
        {
            const string connectionString = "Server=localhost,1433;Database=balta;User ID=sa;Password=1q2w3e4r@#$";

            //using block to dispose this block of code after ran it

            //With ADO.NET
            /*using (var connection = new SqlConnection(connectionString))
            {
                Console.WriteLine("Connected");
                connection.Open();

                using (var command = new SqlCommand())
                {
                    command.Connection = connection;
                    command.CommandType = System.Data.CommandType.Text;
                    command.CommandText = "SELECT [Id], [Title] FROM [Category]";

                    var reader = command.ExecuteReader();
                    while (reader.Read())
                    {
                        Console.WriteLine($"{reader.GetGuid(0)} - {reader.GetString(1)}");
                    }
                }
            }*/

            //With Dapper


            using (var connection = new SqlConnection(connectionString))
            {
                UpdateCategory(connection);
                ListCategories(connection);
                //CreateCategory(connection);
            }
        }

        static void ListCategories(SqlConnection connection)
        {
            var categories = connection.Query<Category>("SELECT [Id], [Title] FROM [Category]");
            foreach (var item in categories)
            {
                Console.WriteLine($"{item.Id} - {item.Title}");
            }
        }

        static void CreateCategory(SqlConnection connection)
        {
            var category = new Category();

            category.Id = Guid.NewGuid();
            category.Title = "Amazon AWS";
            category.Url = "amazon-aws";
            category.Summary = "Amazon Web Service";
            category.Order = 8;
            category.Description = "Treinamento Amazon Aws";
            category.Featured = false;

            var insertSql = $@"INSERT  
                                [Category] 
                            VALUES (
                                @Id, 
                                @Title, 
                                @Url, 
                                @Summary, 
                                @Order,
                                @Description, 
                                @Featured)";

            //Execute is used to Insert, Update and Delet operations
            var rows = connection.Execute(insertSql, new
            {
                category.Id,
                category.Title,
                category.Url,
                category.Summary,
                category.Order,
                category.Description,
                category.Featured
            });
            Console.WriteLine($"{rows} linhas inseridas.");
        }

        static void UpdateCategory(SqlConnection connection)
        {
            var updateQuery = "UPDATE [category] SET [Title]=@title WHERE [Id]=@id";
            var rows = connection.Execute(updateQuery, new
            {
                id = new Guid("09ce0b7b-cfca-497b-92c0-3290ad9d5142"),
                title = "Backend 2023"
            });
            Console.WriteLine($"{rows} registros atualizados.");
        }
    }
}