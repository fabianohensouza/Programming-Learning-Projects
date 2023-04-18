using System;
using Blog.Models;

namespace Blog
{
    internal class Program
    {
        static void Main(string[] args)
        {
            //Console.WriteLine("Hello World!");

            using (var context = new Data.BlogDataContext())
            {
                var tag = new Tag { Name = "ASP.NET", Slug = "asp-net" };
                context.Tags.Add(tag);
                context.SaveChanges();
            }
        }
    }
}