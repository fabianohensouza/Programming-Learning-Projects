﻿namespace DependencyStore.Models;

public class Order
{
    public Order(
        decimal deliveryFee,
        decimal discount,
        List<Product> products)
    {
        Code = Guid.NewGuid().ToString().ToUpper().Substring(0, 8);
        DeliveryFee = deliveryFee;
        Discount = discount;
        Products = products;
    }

    public string Code { get; set; }
    public DateTime Date { get; set; }
    public decimal DeliveryFee { get; set; }
    public decimal Discount { get; set; }
    public List<Product> Products { get; set; }
    public decimal SubTotal => Products.Sum(x => x.Price);
    public decimal Total => SubTotal - Discount + DeliveryFee;
}