using Store.Domain.Entities;
using Store.Domain.Repositories.Interfaces;

namespace Store.Tests.Repositories
{
    public class FakeCustomerRepository : ICustomerRepository
    {
        public Customer Get(string document)
        {
            if (document == "12345678910")
                return new Customer("Bruce Wayne", "Batman@gotham.com");
            
            return null;
        }
    }
}