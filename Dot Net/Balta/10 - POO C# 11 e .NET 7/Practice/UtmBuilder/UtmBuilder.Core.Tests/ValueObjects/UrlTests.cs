using UtmBuilder.Core.ValueObjects;
using UtmBuilder.Core.ValueObjects.Exceptions;

namespace UtmBuilder.Core.Tests.ValueObjects
{
    [TestClass]
    public class UrlTests
    {
        private const string ValidUrl = "https://www.utmtest.com";
        private const string InvalidUrl = "utmtest";

        [TestMethod]
        [TestCategory("URL Test")]
        [ExpectedException(typeof(InvalidUrlException))]
        public void ShouldReturnAnExceptionWhenUrlIsInvalid()
        {
            new Url(InvalidUrl);
        }

        [TestMethod]
        [TestCategory("URL Test")]
        public void ShouldNotReturnAnExceptionWhenUrlIsInvalid()
        {
            new Url(ValidUrl);
            Assert.IsTrue(true);
        }

        [TestMethod]
        [DataRow(" ", true)]
        [DataRow("http", true)]
        [DataRow("banana", true)]
        [DataRow("https://utm.io", false)]
        public void TestUrl(
            string link,
            bool expectException)
        {
            if (expectException)
            {
                try
                {
                    new Url(link);
                    Assert.Fail();
                }
                catch (InvalidUrlException)
                {
                    Assert.IsTrue(true);
                }
            }
            else
            {
                new Url(link);
                Assert.IsTrue(true);
            }
        }
    }
}