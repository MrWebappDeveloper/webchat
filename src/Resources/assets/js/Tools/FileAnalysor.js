import {Helper} from "./Helper";

export class FileAnalysor
{
    static isImage(format)
    {
        format = format.replace('.', '').trim();

        let imageFormats = ['jpg', 'png', 'jpeg', 'webp', 'webp2', 'svg'];
        console.log(Helper.inArray(format, imageFormats))
        return Helper.inArray(format, imageFormats);
    }

    static format(filename)
    {
        let explode = filename.split('.');

        return explode[explode.length - 1]
    }
}
