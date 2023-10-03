using UtmBuilder.Core.Extensions;
using UtmBuilder.Core.ValueObjects;

namespace UtmBuilder.Core
{
    public class Utm
    {
        public Utm(Url url, Campaign campaign)
        {
            Url = url;
            Campaign = campaign;
        }

        /// <summary>Website link</summary>
        public Url Url { get; }
        /// <summary>Campaign details</summary>
        public Campaign Campaign { get; }

        public override string ToString()
        {
            var segments = new List<string>();
            segments.AddIfNotNull("utm_source", Campaign.Source);
            segments.AddIfNotNull("utm_medium", Campaign.Medium);
            segments.AddIfNotNull("utm_campaign", Campaign.Name);
            segments.AddIfNotNull("utm_id", Campaign.Id ?? string.Empty);
            segments.AddIfNotNull("utm_term", Campaign.Term ?? string.Empty);
            segments.AddIfNotNull("utm_content", Campaign.Content ?? string.Empty);

            return $"{Url.Address}?{string.Join("&", segments)}";
        }
    }
}
