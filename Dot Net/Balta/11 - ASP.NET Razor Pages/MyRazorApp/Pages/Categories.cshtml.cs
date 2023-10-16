using Microsoft.AspNetCore.Mvc.RazorPages;

namespace MyRazorApp.Pages
{
    public class Categories : PageModel
    {
        public List<Category> CategoryList { get; set; } = new();
        public string PageName { get; set; } = "/categorias";
        public int DefaultTake { get; set; } = 30;
        public int MaxData { get; set; } = 1200;
        public string? ReturnHref { get; set; } = null;
        public string? AdvanceHref { get; set; } = null;
        public void OnGet(
             int take = 30,
             int skip = 0)
        {
            GeneratePaging(skip, take);

            var tempData = new List<Category>();

            for (int i = 0; i <= MaxData; i++)
            {
                tempData.Add(
                    new Category(i, $"Categoria-{i}", i * 5.87M)
                );
            }

            CategoryList = tempData
                .Skip(skip).
                Take(take).
                ToList();

        }

        private void GeneratePaging(int skip, int take)
        {
            var advanceSkip = skip + take;
            var advanceTake = DefaultTake;
            var returnSkip = skip - DefaultTake;

            if ((skip - DefaultTake) <= 0)
            {
                returnSkip = 0;
            }

            if ((take + skip) >= MaxData)
            {
                advanceSkip = skip;
                advanceTake = MaxData - skip;
            }

            if (skip != 0)
            {
                ReturnHref = $"{PageName}/{returnSkip}/{DefaultTake}";
            }

            if ((take + skip) < MaxData)
            {
                AdvanceHref = $"{PageName}/{advanceSkip}/{advanceTake}";
            }
        }

        public void OnPost()
        {
        }
    }

    public record Category(
        int Id,
        string Title,
        decimal Price);
}