using System;
using System.Data;
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
                //UpdateCategory(connection);
                //CreateManyCategory(connection);
                //ListCategories(connection);
                //CreateCategory(connection);                
                //ExecuteProcedure(connection);              
                //ExecuteReadProcedure(connection);           
                //ExecuteScalar(connection);
                //ReadView(connection);
                OneToOne(connection);
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

        static void CreateManyCategory(SqlConnection connection)
        {
            var category = new Category();
            category.Id = Guid.NewGuid();
            category.Title = "Amazon AWS";
            category.Url = "amazon-aws";
            category.Summary = "Amazon Web Service";
            category.Order = 9;
            category.Description = "Treinamento Amazon Aws";
            category.Featured = false;

            var category2 = new Category();
            category2.Id = Guid.NewGuid();
            category2.Title = "Categoria Nova";
            category2.Url = "categoria-nova";
            category2.Summary = "Nova categoria";
            category2.Order = 10;
            category2.Description = "Nova categoria de treinamento";
            category2.Featured = true;

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
            var rows = connection.Execute(insertSql, new[]{
                new{
                    category.Id,
                    category.Title,
                    category.Url,
                    category.Summary,
                    category.Order,
                    category.Description,
                    category.Featured
                },
                new{
                    category2.Id,
                    category2.Title,
                    category2.Url,
                    category2.Summary,
                    category2.Order,
                    category2.Description,
                    category2.Featured
                },
            });
            Console.WriteLine($"{rows} linhas inseridas.");
        }

        static void ExecuteProcedure(SqlConnection connection)
        {
            var procedure = "[spDeleteStudent]"; //Without the command 'EXEC' and yhe function parameter
            var pars = new { StudentId = "79b82071-80a8-4e78-a79c-92c8cd1fd052" };
            var affectedRows = connection.Execute(
                procedure,
                pars,
                commandType: CommandType.StoredProcedure);

            Console.WriteLine($"{affectedRows} linhas afetadas.");
        }

        static void ExecuteReadProcedure(SqlConnection connection)
        {
            var procedure = "[spGetCoursesByCategory]"; //Without the command 'EXEC' and yhe function parameter
            var pars = new { CategoryId = "25d510c8-3108-44c2-86c5-924d9832aa8c" };
            var courses = connection.Query<Category>(
                procedure,
                pars,
                commandType: CommandType.StoredProcedure);

            foreach (var item in courses)
            {
                Console.WriteLine($"{item.Id} - {item.Title}");
            }
        }

        static void ExecuteScalar(SqlConnection connection)
        {
            var category = new Category();

            category.Title = "Amazon AWS";
            category.Url = "amazon-aws2";
            category.Summary = "Amazon Web Service2";
            category.Order = 8;
            category.Description = "Treinamento Amazon Aws2";
            category.Featured = false;

            var insertSql = $@"INSERT  
                                [Category]
                            OUTPUT inserted.[Id] 
                            VALUES (
                                NEWID(), 
                                @Title, 
                                @Url, 
                                @Summary, 
                                @Order,
                                @Description, 
                                @Featured) ";

            //Execute is used to Insert, Update and Delet operations
            var id = connection.ExecuteScalar<Guid>(insertSql, new
            {
                category.Title,
                category.Url,
                category.Summary,
                category.Order,
                category.Description,
                category.Featured
            });
            Console.WriteLine($"Id da categoria: {id}.");
        }

        static void ReadView(SqlConnection connection)
        {
            var sql = "SELECT * FROM [vwCourses]";
            var courses = connection.Query(sql);
            foreach (var item in courses)
            {
                Console.WriteLine($"{item.Id} - {item.Title}");
            }
        }

        static void OneToOne(SqlConnection connection)
        {
            var sql = @"
                SELECT 
                    * 
                FROM 
                    [CareerItem] 
                INNER JOIN 
                    [Course] ON [CareerItem].[CourseId] = [Course].[Id]";

            var items = connection.Query<CareerItem, Course, CareerItem>(
                sql,
                (careerItem, course) =>
                {
                    careerItem.Course = course;
                    return careerItem;
                }, splitOn: "Id");

            foreach (var item in items)
            {
                Console.WriteLine($"{item.Title} - Curso: {item.Course.Title}");
            }
        }
    }
}