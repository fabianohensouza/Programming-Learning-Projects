using UtmBuilder.Core.ValueObjects;
using UtmBuilder.Core.ValueObjects.Exceptions;

namespace UtmBuilder.Core.Tests.ValueObjects
{
    [TestClass]
    public class CampaignTets
    {
        [TestMethod]
        [DataRow(" ", " ", " ", true)]
        [DataRow("", "", "", true)]
        [DataRow(null, null, null, true)]
        [DataRow("source", "", "", true)]
        [DataRow("source", "medium", "", true)]
        [DataRow("", "", "name", true)]
        [DataRow("source", "medium", "name", false)]
        public void TestCampaign(
                    string source,
                    string medium,
                    string name,
                    bool expectException)
        {
            if (expectException)
            {
                try
                {
                    new Campaign(source, medium, name);
                    Assert.Fail();
                }
                catch (InvalidCampaignException e)
                //when (e.Message == "Source is invalid")
                {
                    Assert.IsTrue(true);
                }
            }
            else
            {
                new Campaign(source, medium, name);
                Assert.IsTrue(true);
            }
        }
    }
}