using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Net;
using System.IO;
using System.Drawing;
using System.Drawing.Imaging;


namespace SocialCardCreator
{
    /// <summary>
    /// Summary description for GetImage
    /// </summary>
    public class GetImage : IHttpHandler
    {
        public void ProcessRequest(HttpContext context)
        {
            // /GetImage.ashx?url=%2Fimages%2F375*238%2Fb99492515z.1_20150506104034_000_g9tb051g.1-0.jpg
            string url = "http://media.jsonline.com" + GetStringParameter("url");
            Image image = GetImageFromUrl(url);
            byte[] imageBuf = ImageToBytes(image);

            context.Response.ContentType = "image/png";
            context.Response.BinaryWrite(imageBuf);
        }

        private static byte[] ImageToBytes(Image img)
        {
            byte[] byteArray = new byte[0];
            using (MemoryStream stream = new MemoryStream())
            {
                img.Save(stream, System.Drawing.Imaging.ImageFormat.Png);
                stream.Close();
                byteArray = stream.ToArray();
            }
            return byteArray;
        }        
        private static Image GetImageFromUrl(string url)
        {
            HttpWebRequest httpWebRequest = (HttpWebRequest)HttpWebRequest.Create(url);

            using (HttpWebResponse httpWebReponse = (HttpWebResponse)httpWebRequest.GetResponse())
            {
                using (Stream stream = httpWebReponse.GetResponseStream())
                {
                    return Image.FromStream(stream);
                }
            }
        }
        private string GetStringParameter(string name)
        {
            string value = "";

            try
            {
                value = HttpContext.Current.Request[name];
                value = HttpContext.Current.Server.UrlDecode(value);
                if (string.IsNullOrEmpty(value)) value = "";
            }
            catch
            {
                value = string.Empty;
            }

            return value;
        }
       
        public bool IsReusable
        {
            get
            {
                return false;
            }
        }
    }
}