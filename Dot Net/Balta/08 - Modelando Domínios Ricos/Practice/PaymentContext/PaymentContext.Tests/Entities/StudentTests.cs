using PaymentContext.Domain.Entities;

namespace PaymentContext.Tests.Entities
{
    [TestClass]
    public class StudentTests
    {
        [TestMethod]
        public void TestMethod1()
        {
            var student = new Student(
                "Fabiano",
                "Souza",
                "089663565",
                "fhs@gmail.com");
        }
    }
}


//20Min