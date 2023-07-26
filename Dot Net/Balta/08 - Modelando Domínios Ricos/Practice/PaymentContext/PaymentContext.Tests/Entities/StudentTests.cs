using PaymentContext.Domain.Entities;
using PaymentContext.Domain.ValueObjects;

namespace PaymentContext.Tests.Entities
{
    [TestClass]
    public class StudentTests
    {
        [TestMethod]
        public void TestMethod1()
        {
            // var subscription = new Subscription(null);
            // var student = new Student("Fabiano", "Souza", "089663565", "fhs@gmail.com");
            // student.AddSubscription(subscription);

            var name = new Name("Teste", "teste");
            foreach (var not in name.Notifications)
            {
                //not.Message;
            }
        }
    }
}