using System;
using System.Collections.Generic;
using System.Linq;
using Balta.ContentContext;
using Balta.ContentContext.Enums;
using Balta.SubscriptionContext;

namespace Balta
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.Clear();

            var articles = new List<Article>();
            articles.Add(new Article("Artigo OOP", "article/orientacao-obj"));
            articles.Add(new Article("Artigo C#", "article/csharp"));
            articles.Add(new Article("Artigo ASP.NET#", "article/aspnet"));

            foreach (var article in articles)
            {
                Console.WriteLine(article.Id);
                Console.WriteLine(article.Title);
                Console.WriteLine(article.Url);
                Console.WriteLine("----------------");
            }

            Console.WriteLine("####################");

            var coursesDotNet = new List<Course>();
            var coursesAngular = new List<Course>();

            var courseOOP = new Course("Fundamentos OOP", "couse/fundamentos-oop", EContentLevel.Beginner);
            var courseCsharp = new Course("Fundamentos C#", "couse/fundamentos-csharp", EContentLevel.Fundamental);
            var courseAspNet = new Course("Fundamentos ASP.NET", "couse/fundamentos-aspnet", EContentLevel.Intermediary);
            var courseJS = new Course("Fundamentos JS", "couse/fundamentos-js", EContentLevel.Fundamental);
            var courseAngular = new Course("Fundamentos Angular", "couse/fundamentos-angular", EContentLevel.Advanced);

            coursesDotNet.Add(courseOOP);
            coursesDotNet.Add(courseCsharp);
            coursesDotNet.Add(courseAspNet);
            coursesAngular.Add(courseJS);
            coursesAngular.Add(courseAngular);

            var careers = new List<Career>();
            var careerDotNet = new Career("Especialista .NET", "carrer/especialista-dotnet");
            var careerItem = new CareerItem(2, "Aprenda .NET", "", courseOOP);
            careerDotNet.Items.Add(careerItem);
            careerItem = new CareerItem(1, "Comece .NET por aqui", "", courseCsharp);
            careerDotNet.Items.Add(careerItem);

            var careerAngular = new Career("Especialista Angular", "carrer/especialista-angular");
            careerItem = new CareerItem(1, "Aprenda Angular", "", null);
            careerAngular.Items.Add(careerItem);

            careers.Add(careerDotNet);
            careers.Add(careerAngular);

            foreach (var career in careers)
            {
                Console.WriteLine(career.Title);

                foreach (var item in career.Items.OrderBy(x => x.Order)) //Ordering the list
                {
                    Console.WriteLine($"{item.Order} - {item.Title}");
                    Console.WriteLine($"{item.Course?.Title} - Nivel: {item.Course?.Level}");

                    foreach (var notification in item.Notifications)
                    {
                        Console.WriteLine($"Notificação: {notification.Property} - {notification.Message}");
                    }
                }

                Console.WriteLine("----------------");
            }

            var payPalSubscription = new PayPalSubscription();
            var pagarmeSubscription = new PagarmeSubscription();
            var student = new Student();
            student.CreateSubscription(payPalSubscription);
            student.CreateSubscription(pagarmeSubscription);

        }
    }
}