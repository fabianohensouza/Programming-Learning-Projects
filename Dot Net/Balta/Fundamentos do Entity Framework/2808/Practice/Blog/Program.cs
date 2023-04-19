using System;
using System.Linq;
using Microsoft.EntityFrameworkCore;

namespace Blog
{
    internal class Program
    {
        static void Main(string[] args)
        {
            //Console.WriteLine("Hello World!");

            using (var context = new Data.BlogDataContext())
            {
                // CREATE
                // var tag = new Tag { Name = "ASP.NET - Advanced", Slug = "aspnet-advanced" };
                // context.Tags.Add(tag);
                // context.SaveChanges();

                //UPDATE
                // var tag = context.Tags.FirstOrDefault(x => x.Id == 3);
                // tag.Name = ".NET";
                // tag.Slug = "dotnet";
                // context.Update(tag);
                // context.SaveChanges();

                // DELETE
                // var tag = context.Tags.FirstOrDefault(x => x.Id == 2);
                // context.Remove(tag);
                // context.SaveChanges();

                // TOLIST
                // var tags = context
                //     .Tags
                //     .Where(x => x.Name.Contains("ASP"))
                //     .ToList();

                // foreach (var tag in tags)
                // {
                //     Console.WriteLine(tag.Name);
                // }

                // ASNOTRACKING
                var tags = context
                    .Tags
                    .AsNoTracking()
                    .Where(x => x.Name.Contains("ASP"))
                    .ToList();

                foreach (var tag in tags)
                {
                    Console.WriteLine(tag?.Name);
                }
            }
        }
    }
}