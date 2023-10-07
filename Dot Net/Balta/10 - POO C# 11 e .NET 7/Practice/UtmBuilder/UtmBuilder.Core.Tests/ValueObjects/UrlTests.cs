using UtmBuilder.Core.ValueObjects;
using UtmBuilder.Core.ValueObjects.Exceptions;

namespace UtmBuilder.Core.Tests.ValueObjects
{
    [TestClass]
    public class UrlTests
    {
        private string ValidUrl = "https://www.utmtest.com?utm_source=online&utm_medium=course&utm_campaign=poo&utm_id=balta&utm_term=poo-code&utm_content=poo-code-course";
        private string InvalidUrl = "utmtestcomutm_source";

        [TestMethod]
        [TestCategory("URL Test")]
        [ExpectedException(typeof(InvalidUrlException))]
        public void ShouldReturnAnExceptionWhenUrlIsInvalid()
        {
            new Url(ValidUrl);
        }

        [TestMethod]
        [TestCategory("URL Test")]
        public void ShouldNotReturnAnExceptionWhenUrlIsInvalid()
        {
            new Url(ValidUrl);
            Assert.IsTrue(true);
        }
    }
}