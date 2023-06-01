using System.ComponentModel.DataAnnotations;

namespace Blog.ViewModels.Categories
{
    public class EditorCategoryViewModel
    {
        [Required(ErrorMessage = "O nome é obrigatório!")]
        [StringLength(60, MinimumLength = 3, ErrorMessage = "Tamanho entre 3 e 60")]
        public string Name { get; set; }
        [Required(ErrorMessage = "O slug é obrigatório!")]
        public string Slug { get; set; }
    }
}
