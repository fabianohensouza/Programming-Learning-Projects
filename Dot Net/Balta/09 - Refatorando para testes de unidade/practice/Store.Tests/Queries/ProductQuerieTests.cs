using Store.Domain.Entities;
using Store.Domain.Queries;
using Store.Tests.Repositories;

namespace Store.Tests.Queries
{
    [TestClass]
    public class ProductQuerieTests
    {
        private IList<Product> _products;

        public ProductQuerieTests()
        {
            _products = new List<Product>();
            _products.Add(new Product("Produto 01", 10, true));
            _products.Add(new Product("Produto 02", 10, true));
            _products.Add(new Product("Produto 03", 10, true));
            _products.Add(new Product("Produto 04", 10, false));
            _products.Add(new Product("Produto 05", 10, false));
        }
            
        [TestMethod]
        [TestCategory("Queries")]
        public void DadaConsultaDeProdutosAtivosDeveRetornar3()
        {
            var guids = new List<Guid>();
            guids.Add(Guid.NewGuid());
            var products = new FakeProductRepository().Get(guids);
            
            var result = products.AsQueryable().Where(ProductQuery.GetActiveProducts());
            Assert.AreEqual( result.Count(), 3);
        }
        
        [TestMethod]
        [TestCategory("Queries")]
        public void DadaConsultaDeProdutosInativosDeveRetornar2()
        {
            var result = _products.AsQueryable().Where(ProductQuery.GetInactiveProducts());
            Assert.AreEqual( result.Count(), 2);
        }
    }
}