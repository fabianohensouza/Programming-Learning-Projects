using Store.Domain.Entities;
using Store.Domain.Repositories.Interfaces;

namespace Store.Tests.Repositories
{
    public class FakeDiscountRepository : IDiscountRepository
    {
        public Discount Get(string code)
        {
            if (code == "12345")
                return new Discount(10, DateTime.Now.AddDays(5));
                
            if (code == "98765")
                return new Discount(20, DateTime.Now.AddDays(-5));
            
            return null;
        }
    }
}