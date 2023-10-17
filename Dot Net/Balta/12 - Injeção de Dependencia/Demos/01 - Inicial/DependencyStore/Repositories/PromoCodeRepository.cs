using Dapper;
using DependencyStore.Models;
using DependencyStore.Repositories.Contracts;
using Microsoft.Data.SqlClient;

namespace DependencyStore.Repositories
{
    public class PromoCodeRepository : IPromoCodeRepository
    {
        private readonly SqlConnection _connection;
        public PromoCodeRepository(SqlConnection connection)
            => _connection = connection; //Expression body

        public async Task<PromoCode?> GetPromoCodeAsync(string promoCode)
        {
            const string query = "SELECT * FROM PROMO_CODES WHERE CODE=@code";
            await _connection
                .QueryFirstAsync<PromoCode>(query, new
                {
                    code = promoCode
                });

            var promo = await _connection.QueryFirstAsync<PromoCode>(query, new { code = promoCode });
            return (promo.ExpireDate > DateTime.Now) ? promo : null;
            /*var discount = (promo.ExpireDate > DateTime.Now) ? promo.Value : 0M;

            return discount;*/
        }
    }
}