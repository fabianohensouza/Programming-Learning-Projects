using Microsoft.AspNetCore.Mvc.RazorPages;

namespace MyRazorApp.Pages
{
    public class Categories : PageModel
    {
        public List<Category> CategoryList { get; set; } = new();
        public int AdvanceTake { get; set; }
        public int DefaultTake { get; set; } = 30;
        public int MaxData { get; set; } = 1200;
        //public int ReturnSkip { get; set; }
        //public int AdvanceSkip { get; set; }
        public string? ReturnHref { get; set; } = null;
        public string? AdvanceHref { get; set; } = null;
        public void OnGet(
            int skip = 0,
            int take = 30)
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
            var returnSkip = ((skip - DefaultTake) <= 0) ? 0 : skip - DefaultTake;

            if ((take + skip) >= MaxData)
            {
                advanceSkip = skip;
                advanceTake = MaxData - skip;
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