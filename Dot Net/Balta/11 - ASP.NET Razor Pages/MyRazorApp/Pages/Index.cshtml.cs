using Microsoft.AspNetCore.Mvc.RazorPages;

namespace MyRazorApp.Pages
{
    public class Index : PageModel
    {
        private readonly ILogger<Index> _logger;

        public List<Category> Categories { get; set; } = new();

        public Index(ILogger<Index> logger)
        {
            _logger = logger;
        }

        public async Task OnGet()
        {
            await Task.Delay(1000);

            for (int i = 0; i <= 100; i++)
            {
                Categories.Add(
                    new Category(i, $"Categoria-{i}", i * 5.87M)
                );
            }

        }

        public void OnPost()
        {
        }
    }
}

public record Category(
    int Id,
    string Title,
    decimal Price);